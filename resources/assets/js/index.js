

import components from './components';


const options = {
    components,
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