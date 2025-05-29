import prettier from 'eslint-config-prettier';
import pluginVue from 'eslint-plugin-vue';

export default [
    ...pluginVue.configs['flat/recommended'],
    prettier,
    {
        ignores: ['vendor', 'node_modules', 'public', 'bootstrap/ssr', 'tailwind.config.js', 'resources/js/components/ui/*'],
    },
    {
        rules: {
            // override/add rules settings here, such as:
            // 'vue/no-unused-vars': 'error'
        },
        languageOptions: {
            sourceType: 'module',
            globals: {
                ...globals.browser
            }
        }
    }
];
