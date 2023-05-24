export const DISPLAY_TASK = 'task';
export const DISPLAY_LIST = 'list';
export const DISPLAY_CONTENT = 'content';

export function isTaskDisplay(display) {
    return display === DISPLAY_TASK;
}

export function isListDisplay(display) {
    return display === DISPLAY_LIST;
}

export function isContentDisplay(display) {
    return display === DISPLAY_CONTENT;
}