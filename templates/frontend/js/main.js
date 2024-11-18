function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
      "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
  }


// объявляем объект примеси
const PlatformMixin = {    
    created() {
        
    },
    data(){
        return { 
          user_id: 0,
          User: JSON.parse(localStorage.user)
         }
    },
    methods: {
        go_auth_page()
        {
            window.location = '/login';
        },
        // обертка над запросами с учетом двухтокеновой авторизации
        dataQuery(q_opts, method_OK, method_Error){
            axios(q_opts).then( response => {
                if(response.data.tstate=='expired')
                    {
                        axios.get(`/api/v1/auth/refresh`, {headers : { Refresh : localStorage.refresh_token }}).then((resp) => {
                            localStorage.access_token = resp.data.accessToken;                    
                            localStorage.refresh_token = resp.data.refreshToken;   
                            setCookie('access_token', localStorage.access_token); 
                            axios(q_opts)
                                .then( _response => method_OK(_response))
                                .catch(_resp => method_Error(_resp))
                        }).catch( err => { this.go_auth_page(); })
                    }
                    else if(response.data.tstate=='invalid')
                    {
                        this.go_auth_page();
                    }
                    else
                    {
                        method_OK(response)  
                    }
            }).catch((err) => { console.log(err); })
        },

        logout() {
            if(confirm('Выйти из аккаунта'))
            {
                    axios.get(`/api/v1/auth/logout`, {headers : { Refresh : localStorage.refresh_token }}).then((resp) => {
                    window.location = '/login';
                    localStorage.clear();   
                    setCookie('access_token', null); 
                }).catch( err => {  
                    console.log('Could not logout');
                })
            }
            
        }       
    
  }
  
}



export {setCookie, getCookie, PlatformMixin}; 