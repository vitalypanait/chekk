import { EntityApi } from '../entity';

export class UserApi extends EntityApi {
    async get() {
        return await this.request('get', `/api/v1/user`);
    }
}
