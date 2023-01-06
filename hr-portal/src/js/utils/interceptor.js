import axios from 'axios';
import { decryptData } from './encrypt';
import { getCookie, deleteCookie, removeAllLocalStorage } from '../composables';
// import { routeNames } from '../../js/routeNames';

export default function interceptorSetup() {
    axios.interceptors.request.use(function(config) {
            //Decrypt Token
            let mkLocalData = getCookie('token');
            let pwdResetToken = getCookie('pwdResetToken');
            if (!mkLocalData) {
                mkLocalData = 'null';
                // Error()
            }
            mkLocalData = (!pwdResetToken ? mkLocalData : pwdResetToken);
            const secretTokenKey = process.env.KEY || 'myscecretkey';
            const token = decryptData(mkLocalData, secretTokenKey);

            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }

            return config;
        },
        function() {
            return Promise.reject();
        });

    axios.interceptors.response.use(
        response => response,
        error => {
            // Error
            const { response: { status } } = error;
            // const { config, response: { status } } = error;

            if (status === 401) {
                // Unauthorized request: maybe access token has expired!
                //return refreshAccessToken(config);
                // console.log(config);
                // console.log('Caught the session error');
                axios.post('http://localhost:8000/api/logout', null)
                // axios.post('https://theportal.scala-apac.org/HR_Engine/api', null)
                    .then(() => {
                        this.$router.push({ path: '/' });
                    }).catch((error) => {
                        this.errors = error.response.data.errors;
                    }).finally(() => {
                        // localStorage.removeItem('token');
                        deleteCookie('token');
                        removeAllLocalStorage();
                        location.reload();
                    });
            } else {
                return Promise.reject(error);
            }
        });
}