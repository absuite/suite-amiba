<?php

namespace Suite\Amiba\Http\Controllers;
use DB;
use Excel;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\File;
use Illuminate\Http\Request;
use Storage;
use Suite\Amiba\Jobs;
use Suite\Amiba\Models;
use Suite\Cbo\Models as CboModels;
use Validator;

class DataDocController extends Controller {
	public function index(Request $request) {
		$pageSize = $request->input('size', 10);
		$query = Models\DataDoc::with('purpose', 'fm_group', 'to_group', 'period', 'element');
		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function showLines(Request $request, string $id) {
		$pageSize = $request->input('size', 10);
		$withs = ['trader', 'item_category', 'item', 'unit'];
		$query = Models\DataDocLine::with($withs);
		$query->where('doc_id', $id);

		$sortField = $request->input('sortField');
		$sortOrder = $request->input('sortOrder', 'asc');
		if ($sortField) {
			$query->orderBy(in_array($sortField, $withs) ? $sortField . '_id' : $sortField, $sortOrder);
		}
		$data = $query->paginate($pageSize);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DataDoc::with('purpose', 'fm_group', 'to_group',
			'period', 'element', 'currency');
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	private function importData(Request $request, $rows, $columns) {
		$collect = collect($rows);
		$grouped = $collect->reject(function ($item) {
			return empty($item['doc_no']);
		})->map(function ($item) {
			$item['doc_no'] = $item['doc_no'] . '';
			return $item;
		})->groupBy('doc_no')
			->toArray();
		foreach ($grouped as $gk => $items) {
			$head = $this->importHeadData($request, $items[0]);
			if (!$head) {
				continue;
			}
			$hasLines = false;
			foreach ($items as $ik => $item) {
				$line = collect($item)->filter(function ($v, $k) {
					return starts_with($k, 'line.');
				})->mapWithKeys(function ($item, $key) {
					return [str_after($key, 'line.') => $item];
				})->all();

				if (count($line) && !(empty($item['qty']) && empty($item['money']))) {
					$this->importLineData($request, $head, $line);
					$hasLines = true;
				}
			}
			if ($hasLines) {
				$job = new Jobs\AmibaDataDocMoneyJob($head->id);
				$job->handle();
			}
		}
	}
	private function importHeadData(Request $request, $data) {
		$input = array_only($data, ['doc_no', 'doc_date', 'money', 'src_id', 'src_no', 'memo']);

		$input = InputHelper::fillEnum($input, $data, [
			'use_type' => 'suite.amiba.doc.use.type.enum',
			'src_type' => 'suite.amiba.doc.src.type.enum',
		]);
		$ent_id = $request->oauth_ent_id;
		$input = InputHelper::fillEntity($input, $data, [
			'purpose' => function ($value) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return Models\Purpose::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'fm_group' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Models\Group::where('ent_id', $ent_id)
					->where('purpose_id', $input['purpose_id'])
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'to_group' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Models\Group::where('ent_id', $ent_id)
					->where('purpose_id', $input['purpose_id'])
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'period' => function ($value, $input) use ($ent_id) {
				if (empty($input['doc_date']) || empty($input['purpose_id'])) {
					return false;
				}
				$query = DB::table('suite_cbo_period_accounts as pa');
				$query->join('suite_amiba_purposes as p', 'pa.calendar_id', '=', 'p.calendar_id');
				$query->where('p.id', $input['purpose_id']);
				$query->where('p.ent_id', $ent_id);
				$query->whereRaw("'" . $input['doc_date'] . "' between pa.from_date and pa.to_date");
				return $query->value('pa.id');
			},
			'element' => function ($value, $input) use ($ent_id) {
				if (empty($value) || empty($input['purpose_id'])) {
					return false;
				}
				return Models\Element::where('ent_id', $ent_id)
					->where('purpose_id', $input['purpose_id'])
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'currency' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Currency::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
		]);
		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'doc_date' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $ent_id;
		$input['state_enum'] = 'opened';
		return Models\DataDoc::create($input);
	}
	private function importLineData(Request $request, $head, $data) {
		$input = array_only($data, ['qty', 'price', 'money', 'expense_code', 'account_code', 'memo']);

		$ent_id = $request->oauth_ent_id;
		$input = InputHelper::fillEntity($input, $data, [
			'trader' => function ($value) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Trader::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'item_category' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\ItemCategory::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'item' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Item::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'mfc' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Mfc::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'project' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Project::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
			'unit' => function ($value, $input) use ($ent_id) {
				if (empty($value)) {
					return false;
				}
				return CboModels\Unit::where('ent_id', $ent_id)
					->where(function ($query) use ($value) {
						$query->where('code', $value)->orWhere('name', $value);
					})->value('id');
			},
		]);
		$input['doc_id'] = $head->id;
		$input['ent_id'] = $ent_id;
		if (empty($input['money'])) {
			$input['money'] = 0;
		}
		if (empty($input['price'])) {
			$input['price'] = 0;
		}
		if (empty($input['qty'])) {
			$input['qty'] = 0;
		}
		return Models\DataDocLine::create($input);
	}
	public function import(Request $request) {
		$files = File::storage($request, 'files', 'import', 'local');
		if ($files) {
			foreach ($files as $key => $file) {
				$disk = Storage::disk($file->disk);
				$path = $disk->path($file->path);
				Excel::load($path, function ($reader) use ($request) {
					$results = $reader->all();
					foreach ($results as $sheet) {
						$columns = array_where($sheet->getHeading(), function ($value) {
							return is_string($value) && $value;
						});
						$rowsData = [];
						foreach ($sheet as $key => $row) {
							if ($key > 0) {
								$rowsData[] = $row->only($columns)->all();
							}
						}
						$this->importData($request, $rowsData, $columns);
					}
				});
			}
		}
		return $this->toJson(true);
	}
	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = $request->all();
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'fm_group', 'to_group', 'period', 'element', 'currency']);

		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'doc_date' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = $request->oauth_ent_id;
		$data = Models\DataDoc::create($input);

		$this->storeLines($request, $data->id);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->only(['doc_no', 'doc_date', 'state_enum', 'money', 'use_type_enum', 'created_by', 'src_type_enum', 'src_id', 'src_no', 'memo']);
		$input = InputHelper::fillEntity($input, $request, ['purpose', 'fm_group', 'to_group', 'period', 'element', 'currency']);

		$validator = Validator::make($input, [
			'doc_no' => 'required',
			'doc_date' => 'required',
			'purpose_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		if (empty($input['money'])) {
			$input['money'] = 0;
		}

		Models\DataDoc::where('id', $id)->update($input);
		$this->storeLines($request, $id);
		return $this->show($request, $id);
	}
	private function storeLines(Request $request, $headId) {
		$lines = $request->input('lines');
		$fillable = ['qty', 'price', 'money', 'expense_code', 'account_code', 'memo'];
		$entityable = ['trader', 'item_category', 'item', 'mfc', 'project', 'unit'];

		if ($lines && count($lines)) {
			foreach ($lines as $key => $value) {
				if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					$data['doc_id'] = $headId;
					$data['ent_id'] = $request->oauth_ent_id;
					Models\DataDocLine::create($data);
					continue;
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
					$data = array_only($value, $fillable);
					$data = InputHelper::fillEntity($data, $value, $entityable);
					if (empty($data['money'])) {
						$data['money'] = 0;
					}
					if (empty($data['price'])) {
						$data['price'] = 0;
					}
					if (empty($data['qty'])) {
						$data['qty'] = 0;
					}
					Models\DataDocLine::where('id', $value['id'])->update($data);
				}
				if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
					Models\DataDocLine::destroy($value['id']);
					continue;
				}
			}
		}
		$job = new Jobs\AmibaDataDocMoneyJob($headId);
		$job->handle();
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\DataDocLine::whereIn('doc_id', $ids)->delete();
		Models\DataDoc::destroy($ids);
		return $this->toJson(true);
	}
}
