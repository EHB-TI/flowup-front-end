<template>
    <div>
        <div>
            <h1>Edit an event</h1>
        </div>

        <div>
            <b-form @submit.prevent="editEvent">
                <b-form-group
                    id="input-group-1"
                    label="Name"
                    label-for="name">

                    <b-form-input
                    id="input-name"
                    v-model="event.name"
                    type="text"
                    placeholder="Enter name (max. 30 characters)"
                    required>
                    </b-form-input>
                </b-form-group>

                <b-form-group
                    id="input-group-2"
                    label="Description"
                    label-for="description">

                    <b-form-textarea
                    id="input-description"
                    v-model="event.description"
                    type="text"
                    placeholder="Enter description (max. 50 characters)"
                    rows="3"
                    max-rows="6"
                    required>
                    </b-form-textarea>
                </b-form-group>
                
                <b-form-group
                    id="input-group-3"
                    label="Starts at"
                    label-for="startEvent">

                    <b-form-datepicker id="example-datepicker" v-model="event.startEvent" class="mb-2"></b-form-datepicker>
                </b-form-group>

                <b-form-group
                    id="input-group-4"
                    label="Ends at"
                    label-for="endEvent">

                    <b-form-datepicker id="example-datepicker" v-model="event.endEvent" class="mb-2"></b-form-datepicker>
                </b-form-group>


                <b-form-group
                    id="input-group-5"
                    label="Location"
                    label-for="location">

                    <b-form-input
                    id="input-location"
                    v-model="event.location"
                    type="text"
                    placeholder="Enter location..."
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
                event: {},
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
