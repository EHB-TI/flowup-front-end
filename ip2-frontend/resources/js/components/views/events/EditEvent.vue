<template>
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
</template>
<style scoped>

</style>
