//const { createApp, ref, onMounted } = Vue

import {setCookie, getCookie} from './common.js';


const Login = {

    data(){
        return { 
          login: '',
          password: ''
         }
    },
    template: `
<div class="row">
        <div class="col">
      
        </div>
        <div class="col">
            <form action="">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Логин</label>
                <input type="text" class="form-control" name="login" v-model="login" />
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Пароль</label>
                <input type="password" class="form-control" name="password" v-model="password" />
            </div>
            <input type="submit" value="Войти" class="btn btn-outline-primary" @click.prevent="Auth()" />
            </form>
        </div>
        <div class="col">
        
        </div>
    </div>
    `,
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
                    this.$root.load_pages();                    
                    this.$router.push('/');
                    this.$forceUpdate();
                    //document.location = getCookie('URL');
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

    },    
    created () {
        document.title = 'Вход в систему';
    }
  }

  
export {Login}; 