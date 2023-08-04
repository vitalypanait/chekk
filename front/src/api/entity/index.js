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
        return await this.request('get', `/api/v1/board/${id}/label`);
    }

    async updateTitle(id, params) {
        return await this.request('put', `/api/v1/board/${id}`, params);
    }

    async addTask(boardId, params) {
        return await this.request('post', `/api/v1/board/${boardId}/task/`, params);
    }

    async updateTask(boardId, taskId, params) {
        return await this.request('put', `/api/v1/board/${boardId}/task/${taskId}`, params);
    }

    async deleteTask(boardId, taskId) {
        return await this.request('delete', `/api/v1/board/${boardId}/task/${taskId}`);
    }

    async archiveTask(boardId, taskId) {
        return await this.request('put', `/api/v1/board/${boardId}/task/archive/${taskId}`);
    }

    async restoreTask(boardId, taskId) {
        return await this.request('delete', `/api/v1/board/${boardId}/task/archive/${taskId}`);
    }

    async addComment(boardId, params) {
        return await this.request('post', `/api/v1/board/${boardId}/comment/`, params);
    }

    async deleteComment(boardId, commentId) {
        return await this.request('delete', `/api/v1/board/${boardId}/comment/${commentId}`);
    }

    async setLabel(boardId, params) {
        return await this.request('post', `/api/v1/board/${boardId}/task/label`, params);
    }

    async deleteTaskLabel(boardId, id) {
        return await this.request('delete', `/api/v1/board/${boardId}/task/label/${id}`);
    }

    async updatePositions(id, params) {
        return await this.request('put', `/api/v1/board/${id}/task/position/`, params);
    }

    async deleteLabel(boardId, id) {
        return await this.request('delete', `/api/v1/board/${boardId}/label/${id}`);
    }

    async addLabel(boardId, params) {
        return await this.request('post', `/api/v1/board/${boardId}/label/`, params);
    }

    async updateLabel(boardId, params) {
        return await this.request('put', `/api/v1/board/${boardId}/label/`, params);
    }
}
