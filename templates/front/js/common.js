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

const MenuMixin = {
    template: `
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Главная</a>
        </li>
       
        <li class="nav-item">
          <a class="nav-link" href="#" >Акты</a>
        </li>
      </ul>
    </div>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <li class="d-flex"> Hello, {{User.login}}</li>
        <li class="d-flex">
            <a class="nav-link" href="#" @click.prevent="logout">Выход</a>
        </li>
    </div>
  </div>
</nav>      
    `
}
// объявляем объект примеси
const PlatformMixin = {    
    created() {
        
    },
    data(){
        var user_var = ('user' in localStorage) ? JSON.parse(localStorage.user) : "";
        return { 
          user_id: 0,
          User: user_var
         }
    },
    methods: {
        go_auth_page()
        {
            localStorage.clear();   
            setCookie('access_token', null); 
            this.$root.load_pages();
            this.$router.push('/login');
            
          //  window.location = '/login';
        },
        // обертка над запросами с учетом двухтокеновой авторизации
        dataQuery(q_opts, method_OK, method_Error){
            axios(q_opts).then( response => {
                if(response.data.tstate=='expired')
                    {
                        axios.get(`/api/v1/auth/refresh`, {headers : { Refresh : localStorage.refresh_token }}).then((resp) => {
                            localStorage.access_token = resp.data.accessToken;                    
                            localStorage.refresh_token = resp.data.refreshToken;   
                          //  this._computedWatchers.Authed.run();
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

           
    
  }
  
}



export {setCookie, getCookie, PlatformMixin, MenuMixin}; 