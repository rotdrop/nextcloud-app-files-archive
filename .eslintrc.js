module.exports = {
  extends: [
    '@nextcloud',
  ],
  globals: {
    __webpack_nonce__: true,
    __webpack_public_path__: true,
  },
  rules: {
    'no-tabs': ['error', { allowIndentationTabs: false }],
    indent: ['error', 2],
    'no-mixed-spaces-and-tabs': 'error',
    'vue/html-indent': ['error', 2],
    semi: ['error', 'always'],
    'no-console': 'off',
    'n/no-missing-require': [
      'error', {
        resolvePaths: [
          './src',
          './style',
          './',
          './img',
        ],
        tryExtensions: ['.js', '.ts', '.json', '.node', '.css', '.scss', '.xml', '.vue', '.svg'],
      },
    ],
    // Do allow line-break before closing brackets
    'vue/html-closing-bracket-newline': ['error', { singleline: 'never', multiline: 'always' }],
  },
  overrides: [
    {
      files: ['*.vue'],
      rules: {
        semi: ['error', 'never'],
      },
    },
  ],
};
