<template>
        <div>
                <table>
                    <thead>
                        <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                        <th scope="row">{{ user.firstName  }}</th>  
                        <td>{{ user.lastName }}</td> 
                        <td>{{ user.email }}</td>  
                        </tr>
                    </tbody>
                </table>
        </div>
</template>
<script>
export default {
    data() { 
        return {
            users: {},
        }
    },
    created() {
            this.axios
            .get('http://127.0.0.1:8000/api/users')
            .then(response => {
                this.users = response.data;
            });
    }
}
</script>
<style scoped>

</style>