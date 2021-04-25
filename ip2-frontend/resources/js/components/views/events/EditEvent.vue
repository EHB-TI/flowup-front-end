<template>
    <div>
        <div>
            <h1>Edit event</h1>
        </div>
        <div>
            <b-form @submit.prevent="editEvent">
                <b-form-group
                    id="input-group-1"
                    label="Name:"
                    label-for="name">

                    <b-form-input
                    id="input-name"
                    v-model="event.name"
                    type="text"
                    placeholder="Enter name..."
                    required>
                    </b-form-input>
                </b-form-group>

                <b-button type="submit" variant="primary">Submit</b-button>
            </b-form>
        </div>
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
            editEvent() {
                this.axios
                    .patch(`http://localhost:8000/api/events/${this.$route.params.id}`, this.event)
                    .then((res) => {
                        this.$router.push({ name: 'home' });
                    });
            }
        }
    }
</script>
<style scoped>

</style>
