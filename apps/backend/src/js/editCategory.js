import Vue from 'vue';

new Vue({
    el: '#submit-category',
    data:{
        post_data: {
            name: '',
            id: ''
        },
        categories: JSON.parse('<?= $category_data ?>'),
        errors: [],
    },
    props: [
        'HasError',
        'Message',
    ],

    methods: {
        updateCategory: function () {

            let category = this.post_data;
            this.$http.post('/admin/categories/updateCategory', category, {}).then((response) => {
                if(response.data.error == true) {
                    this.HasError = true;
                    this.errors = response.data.errors;
                    this.Message  = response.data.message;
                } else{
                    this.errors = [];
                    this.HasError = false;
                    this.Message = response.data.message;
                }
            });
        }
    }
})