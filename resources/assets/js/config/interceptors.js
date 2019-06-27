import Vue from 'vue';
import _ from 'lodash';

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        switch (error.response.status) {
            case 500:
                Vue.prototype.$awn.alert('Whoops, something went wrong');
                break;

            case 422:
                let errors = _.values(error.response.data.errors);
                Vue.prototype.$awn.warning(_.first(errors)[0]);
                break;

            case 401:
                Vue.prototype.$awn.warning('Authorization is required');
                break;
        }

        return Promise.reject(error);
    }
);