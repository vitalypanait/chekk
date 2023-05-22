const STATUS_CREATED = 'created';
const STATUS_COMPLETED = 'completed';
const STATUS_PAUSED = 'paused';
const STATUS_PROCESSING = 'processing';

const STATUSES = [
    { value: STATUS_CREATED, icon: 'mdi-circle-outline', color: 'black' },
    { value: STATUS_COMPLETED, icon: 'mdi-checkbox-marked-circle', color: 'green' },
    { value: STATUS_PAUSED, icon: 'mdi-stop-circle', color: 'red' },
    { value: STATUS_PROCESSING, icon: 'mdi-clock-time-four', color: 'blue' },
];

export function getStatuses() {
    return STATUSES;
}

export function getStatusColor(status) {
    let color = 'grey';

    STATUSES.forEach(function(element) {
        if (element.value === status) {
            color = element.color;

            return false;
        }
    });

    return color;
}

export function getStatusIcon(status) {
    let icon = 'mdi-circle-outline';

    STATUSES.forEach(function(element) {
        if (element.value === status) {
            icon = element.icon;

            return false;
        }
    });

    return icon;
}

export function getNextStatus(status) {
    let value = STATUS_PAUSED;

    if (status === STATUS_CREATED) {
        value = STATUS_PROCESSING
    } else if (status === STATUS_PROCESSING) {
        value = STATUS_COMPLETED
    } else if (status === STATUS_COMPLETED) {
        value = STATUS_PAUSED
    } else if (status === STATUS_PAUSED) {
        value = STATUS_CREATED
    }

    return value;
}
