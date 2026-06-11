import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route, ZiggyVue } from 'ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'ServiceHub';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const ziggyConfig = props.initialPage.props.ziggy;
        window.Ziggy = ziggyConfig;
        window.route = (name, params, absolute) => route(name, params, absolute, ziggyConfig);
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, ziggyConfig)
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
