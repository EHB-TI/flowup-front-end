<template>
    <!-- <div>
        <b-avatar style="float:left;" class="absolute mr-2 top-0 left-0" src="https://placekitten.com/300/300" size="6rem"></b-avatar>
        <h1 style=" margin-top: 18px; float:left;">Souheib Touri</h1>
        <b-button @click="fetchUsers()">Fetch users</b-button>
    </div> -->

    <div>
        <a-layout>
            <a-layout-header style="background:white; height: 120px; padding: 15px;">
                <a-avatar style="float:left;" :size="86" icon="user" src="https://cdn.dribbble.com/users/986811/screenshots/4601947/argentina_leo_messi.png?compress=1&resize=400x300"/>
                <h1 style="float:left; padding:15px;">Souheib Touri</h1>
                
                <b-button variant="primary" style="float:right; padding:10px; margin-top: 20px; margin-right:10px;">
                    Edit profile
                </b-button>
            </a-layout-header>
            <a-layout>
                <a-layout-content style="background: white; padding: 15px;">
                    <h3>Your information</h3>
                    <a-descriptions bordered>
                        <a-descriptions-item label="Email">
                        souheibtouri@hotmail.com
                        </a-descriptions-item>
                        <a-descriptions-item label="Birthday">
                        22-03-1999
                        </a-descriptions-item>
                        <a-descriptions-item label="Study">
                        TI
                        </a-descriptions-item>
                    </a-descriptions>
                </a-layout-content>
                <a-layout-sider style="background:white; padding:10px;">
                    
                </a-layout-sider>
            </a-layout>
        </a-layout>
    </div>
    
</template>
<script>
export default {
    data() {
        return {
            users: {}
        }
    },
    methods: {
        fetchUsers() {
            axios
            .get('http://127.0.0.1:8000/api/users')
            .then(function (response) {
                // handle success
                console.log(response.data);
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            });
        },
        created() {
            this.fetchUsers();
        }

    }
}
</script>

<style scoped>

</style>
