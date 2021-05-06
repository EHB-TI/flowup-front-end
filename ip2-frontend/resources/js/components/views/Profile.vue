<template>
    <div>
        <b-avatar style="float:left;" class="absolute mr-2 top-0 left-0" src="https://placekitten.com/300/300" size="6rem"></b-avatar>
        <h1 style=" margin-top: 18px; float:left;">Souheib Touri</h1>
        <b-button @click="fetchUsers()">Fetch users</b-button>
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
