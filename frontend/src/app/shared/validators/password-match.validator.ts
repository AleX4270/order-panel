import { AbstractControl, ValidationErrors, ValidatorFn } from "@angular/forms";

export function validatePasswordMatch(): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
        const parent = control.parent;
        if(!parent) {
            return null;
        }

        const password = parent.get('password')?.value;
        const passwordConfirmed = control.value;

        if(
            ((password != null && password != '')
            || (passwordConfirmed != null && passwordConfirmed != ''))
            && (password !== passwordConfirmed)
        ) {
            return { passwordMismatch: true };
        }

        return null;
    }
}