import { AbstractControl, ValidationErrors, ValidatorFn } from "@angular/forms";

export function validatePasswordStrength(): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
        const password = control.value;

        if(password == null || password == '') {
            return null;
        }

        const pattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*\-.,]).{8,}$/;

        if(!pattern.test(password)) {
            return { weakPassword: true };
        }

        return null;
    }
}