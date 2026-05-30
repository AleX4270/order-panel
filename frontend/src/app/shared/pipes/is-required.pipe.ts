import { Pipe, PipeTransform } from '@angular/core';
import { AbstractControl, FormControl, Validators } from '@angular/forms';

@Pipe({
    name: 'isRequired',
})
export class IsRequiredPipe implements PipeTransform {
    transform(value: AbstractControl | null): boolean {
        if (!value) return false;
        return value.hasValidator(Validators.required) || value.hasValidator(Validators.requiredTrue);
    }
}
