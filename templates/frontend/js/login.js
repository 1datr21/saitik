//const { createApp, ref, onMounted } = Vue

import {setCookie, getCookie} from './main.js';


Vue.createApp({

    data(){
        return { 
          login: '',
          password: ''
         }
    },
    methods: {
        
        Auth()
        {
            axios.post(`/api/v1/auth/sign_in`, {
                login: this.login,
                password: this.password
            })
            .then( (response) => {
                console.log("SERVER RESPONSE");
                console.log(response);
                if(response.data.response=='error')
                {
                   this.error_mess = response.data.message;
                }
                else
                {
                    // токен получен
                    localStorage.access_token = response.data.accessToken;                    
                    localStorage.refresh_token = response.data.refreshToken;   
                    setCookie('access_token', localStorage.access_token);
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                    document.location = getCookie('URL');
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        },
       /* MakeAct()
        {
            var myModal = document.getElementById('myModal')
            var myInput = document.getElementById('myInput')

            myModal.addEventListener('shown.bs.modal', function () {
            myInput.focus()
            })
        }*/
    },
    mounted(){  
        console.log(document.cookie);

    }
    
  }).mount('#app')