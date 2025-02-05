import {fixupConfigRules, fixupPluginRules} from "@eslint/compat";
import react from "eslint-plugin-react";
import globals from "globals";
import {fileURLToPath} from "node:url";
import path from "node:path";
import {FlatCompat} from "@eslint/eslintrc";
import js from "@eslint/js";
import reactHooks from "eslint-plugin-react-hooks";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all
});

export default [
    ...fixupConfigRules(compat.extends(
        "eslint:recommended",
        "plugin:react/recommended",
        "plugin:react-hooks/recommended",
    )),
    {
        ignores: ["vendor/**/*", "public/**/*"],
    },
    {
        files: ['**/*.{js,jsx,mjs,cjs,ts,tsx}'],
        ignores: ["vendor/**/*", "public/**/*"],
        plugins: {
            react: fixupPluginRules(react),
            "react-hooks": fixupPluginRules(reactHooks),
        },
        languageOptions: {
            ecmaVersion: 12,
            sourceType: "module",
            parserOptions: {
                ecmaFeatures: {
                    jsx: true
                }
            },
            globals: {
                ...globals.browser
            }
        },
        settings: {
            react: {
                version: "detect",
            },
        },

        rules: {
            // ... any rules you want
            'react/jsx-uses-react': 'off',
            "react/react-in-jsx-scope": "off",
            "no-multiple-empty-lines": ["error", {
                "max": 1
            }],
            "padded-blocks": ["error", "never"]
        }
        // ... others are omitted for brevity
    }
];
