import { EntityApi } from '../entity';

export class BoardApi extends EntityApi {
    async takeOwnership(id) {
        return await this.request('post', `/api/v1/board/${id}/take-ownership`);
    }

    async setPinCode(id, pinCode) {
        return await this.request('post', `/api/v1/board/${id}/pin-code`, {pinCode: pinCode});
    }

    async removePinCode(id) {
        return await this.request('delete', `/api/v1/board/${id}/pin-code`);
    }

    async checkAccess(id) {
        return await this.request('get', `/api/v1/board/${id}/access`);
    }

    async authBoard(id, pinCode) {
        return await this.request('post', `/api/v1/board/${id}/access`, {pinCode: pinCode});
    }
}
