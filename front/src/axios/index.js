import axios from 'axios';

const client = axios.create({
    baseURL: window.location.origin,
    timeout: 0,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-Timezone-Offset': new Date().getTimezoneOffset(),
    },
});

export default client;