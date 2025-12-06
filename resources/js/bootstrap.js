import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// 导入并初始化 Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
