<template>
    <div>
        <h1>Event page (ID: {{ event.id }})</h1>

        <p>Name: {{ event.name }}</p>
        <p>Description: {{ event.description }}</p>

        <b-button @click="deleteEvent(event.id)" >Delete</b-button>
    </div>
</template>
<script>
export default {
        data() {
            return {
                event: {}
            }
        },
        created() {
            this.axios
                .get(`http://localhost:8000/api/events/${this.$route.params.id}`)
                .then((res) => {
                    this.event = res.data;
                });
        },
        methods: {
            deleteEvent(id) {
                this.axios
                    .delete(`http://127.0.0.1:8000/api/events/${this.$route.params.id}`)
                    .then(response => {
                        let i = this.event.map(data => data.id).indexOf(id);
                        this.event.splice(i, 1);
                        this.$router.push({ name: 'home' });
                    });
            },
        }
}
</script>
