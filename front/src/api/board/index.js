import { EntityApi } from '../entity';

export class BoardApi extends EntityApi {
    async takeOwnership(id) {
        return await this.request('post', `/api/v1/board/take-ownership/${id}`);
    }
}
