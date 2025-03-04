import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// createInertiaApp({
//   resolve: (name) => {
//     const pages = import.meta.glob('./Pages/**/*.vue'); 
//     return pages[`./Pages/${name}.vue`]();
//   },
//   setup({ el, App, props, plugin }) {
//     createApp({ render: () => h(App, props) })
//       .use(plugin)
//       .mount(el);
//   },
// });

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue');
    const match = Object.keys(pages).find(path => path.endsWith(`${name}.vue`));
    if (!match) {
      console.error(`Page "${name}" not found in Pages folder`);
      return Promise.reject(`Page "${name}" not found`);
    }

    return pages[match]();
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
});
