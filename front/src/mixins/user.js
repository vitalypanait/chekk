import {UserApi} from '../api/index.js';

const userApi = new UserApi();

export default {
  data() {
    return {
      user: {authorized: false},
    };
  },
  async mounted() {
    this.user = await userApi.get();
  },
  methods: {
    isUserAuthorized() {
      return this.user.authorized
    }
  },
};
