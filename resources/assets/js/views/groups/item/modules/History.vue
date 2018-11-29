<template>

    <table class="table">
        <tbody>
            <tr v-for="history in group.histories">
                <td v-html="history.value"></td>
                <td>{{ history.created_at | datetime }}</td>
            </tr>
        </tbody>
    </table>


</template>

<script>

    import moment from 'moment';

    export default {

        props: ['group'],

        filters: {
            datetime: function(value) {

                return moment(value, "YYYY-MM-DD hh:mm:ss").format("DD.MM.YYYY HH:mm");

            }
        },

    }

</script>