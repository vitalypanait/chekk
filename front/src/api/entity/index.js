import HTTP from '../../axios';

const NotImplementedError = new Error(
    'Method should be implemented in inherited class'
);

export class EntityApi {
    async request(type, path, ...args) {
        const response = await HTTP[type](path, ...args);

        return response.data;
    }

    async getBoard(id) {
        return await this.request('get', `/api/v1/board/${id}`);
    }

    async getLabelsByBoard(id) {
        return await this.request('get', `/api/v1/label/byBoard/${id}`);
    }

    async updateTitle(id, params) {
        return await this.request('put', `/api/v1/board/${id}`, params);
    }

    async addTask(params) {
        return await this.request('post', `/api/v1/task/`, params);
    }

    async updateTask(id, params) {
        return await this.request('put', `/api/v1/task/${id}`, params);
    }

    async deleteTask(id) {
        return await this.request('delete', `/api/v1/task/${id}`);
    }

    async archiveTask(id) {
        return await this.request('put', `/api/v1/task/archive/${id}`);
    }

    async restoreTask(id) {
        return await this.request('delete', `/api/v1/task/archive/${id}`);
    }

    async addComment(params) {
        return await this.request('post', `/api/v1/comment/`, params);
    }

    async deleteComment(id) {
        return await this.request('delete', `/api/v1/comment/${id}`);
    }

    async setLabel(params) {
        return await this.request('post', `/api/v1/task-label/`, params);
    }

    async deleteTaskLabel(id) {
        return await this.request('delete', `/api/v1/task-label/${id}`);
    }

    async updatePositions(params) {
        return await this.request('put', `/api/v1/task/position/`, params);
    }

    async deleteLabel(id) {
        return await this.request('delete', `/api/v1/label/${id}`);
    }

    async addLabel(params) {
        return await this.request('post', `/api/v1/label/`, params);
    }

    async updateLabel(params) {
        return await this.request('put', `/api/v1/label/`, params);
    }
}
