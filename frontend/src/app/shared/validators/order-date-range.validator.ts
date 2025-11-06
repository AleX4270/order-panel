import { AbstractControl, FormGroup, ValidationErrors, ValidatorFn } from "@angular/forms";

export function validateOrderDateRange(): ValidatorFn {
    return (form: AbstractControl): ValidationErrors | null => {
        if(!(form instanceof FormGroup)) {
            return null;
        }

        let dateCreation = form.get('dateCreation')?.value;
        const dateDeadlineField = form.get('dateDeadline');
        const dateCompletedField = form.get('dateCompleted');

        if(!dateCreation) {
            clearError(dateDeadlineField, 'deadlineBeforeCreation');
            clearError(dateCompletedField, 'completedBeforeCreation');
            return null;
        }

        dateCreation = new Date(dateCreation);
        const dateDeadline = dateDeadlineField?.value ? new Date(dateDeadlineField.value) : null;
        const dateCompleted = dateCompletedField?.value ? new Date(dateCompletedField.value) : null;

        if(dateDeadline && dateDeadline < dateCreation) {
            dateDeadlineField?.setErrors({...(dateDeadlineField.errors || {}), deadlineBeforeCreation: true});
            dateDeadlineField?.markAllAsDirty();
        }
        else {
            clearError(dateDeadlineField, 'deadlineBeforeCreation');
        }

        if(dateCompleted && dateCompleted < dateCreation) {
            dateCompletedField?.setErrors({...(dateCompletedField.errors || {}), completedBeforeCreation: true});
            dateCompletedField?.markAllAsDirty();
        }
        else {
            clearError(dateCompletedField, 'completedBeforeCreation');
        }

        return null;
    }

    function clearError(control: AbstractControl | null, key: string): void {
        if(!control || !control.errors) {
            return;
        }

        const { [key]: removedErr, ...errs } = control.errors;
        control.setErrors(Object.keys(errs).length ? errs : null);
    }
}