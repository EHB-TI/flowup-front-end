<template>
    <div>
        <div>
            <h1>Create an event</h1>
        </div>

        <div>
            <b-form @submit.prevent="addEvent">
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
                    label-for="startsAt">

                    <b-form-datepicker id="example-datepicker" v-model="event.startsAt" class="mb-2"></b-form-datepicker>
                </b-form-group>

                <b-form-group
                    id="input-group-4"
                    label="Ends at"
                    label-for="endsAt">

                    <b-form-datepicker id="example-datepicker" v-model="event.endsAt" class="mb-2"></b-form-datepicker>
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
            event: {}
        }
    },
    methods: {
        addEvent() {
            this.axios
                .post('http://127.0.0.1:8000/api/events', this.event)
                .then(response => (
                    this.$router.push({ name: 'home' })
                ))
                .catch(err => console.log(err))
                .finally( () => this.loading = false)
        }
    }
}
</script>
