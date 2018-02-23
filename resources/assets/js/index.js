import components from './components';
import refs from './refs';

const options = {
  components,
  refs,
};

options.install = (Vue) => {
  if (options.installed) {
    console.warn('Vue Material is already installed.');
    return;
  }
  for (let component in options) {
    const componentInstaller = options[component];

    if (componentInstaller && component !== 'install') {
      Vue.use(componentInstaller);
    }
  }
  options.installed = true;
};
export default options;