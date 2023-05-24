import {isContentDisplay, isListDisplay, isTaskDisplay} from "../models/index.js";

export default {
  methods: {
    getMarginDisplay(display) {
      if (isTaskDisplay(display) || isContentDisplay(display)) {
        return 'ml-3'
      } else if (isListDisplay(display)) {
        return 'ml-4'
      }
    },
    getTitleMarginDisplay(display) {
      if (isTaskDisplay(display)) {
        return 'ml-5'
      } else if (isListDisplay(display) || isContentDisplay(display)) {
        return 'ml-3'
      }
    },
    getLabelMarginDisplay(display) {
      if (isTaskDisplay(display) || isListDisplay(display)) {
        return 'ml-13'
      } else if (isContentDisplay(display)) {
        return 'ml-2'
      }
    },
    getEditorMarginDisplay(display) {
      if (isTaskDisplay(display) || isListDisplay(display)) {
        return 'ml-14'
      } else if (isContentDisplay(display)) {
        return 'ml-3'
      }
    },
    getCommentMarginDisplay(display) {
      if (isTaskDisplay(display) || isListDisplay(display)) {
        return 'ml-14'
      } else if (isContentDisplay(display)) {
        return 'ml-3'
      }
    }
  },
};
