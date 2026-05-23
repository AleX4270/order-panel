import { Pipe, PipeTransform } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';

@Pipe({
    name: 'isRequired',
})
export class IsRequiredPipe implements PipeTransform {
    transform(value: FormControl): boolean {
        return value.hasValidator(Validators.required) || value.hasValidator(Validators.requiredTrue);
    }
}
