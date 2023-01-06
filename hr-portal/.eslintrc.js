module.exports = {
  root: true,
  env: {
    node: true
  },
  'extends': [
    'plugin:vue/vue3-essential',
    'eslint:recommended'
  ],
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'linebreak-style': 0,
    'quotes': ['warn','single'],
    'semi': ['warn','always']
  },
  parserOptions: {
    parser: 'babel-eslint'
  },
  'globals': {
    'io': false
  }
};
