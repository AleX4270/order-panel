import { Component, computed, effect, input, InputSignal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TileSize, TileType } from '../../types/tile.types';

@Component({
    selector: 'app-tile',
    imports: [CommonModule],
    template: `
        <div [ngClass]="classList()">
            <ng-content/>
        </div>
    `,
    styles: [``]
})
export class TileComponent {
    public type: InputSignal<TileType> = input<TileType>('primary');
    public size: InputSignal<TileSize> = input<TileSize>('extra-small');
    public isSoft: InputSignal<boolean> = input<boolean>(false);
    public isOutlined: InputSignal<boolean> = input<boolean>(false);

    protected classList = computed(() => {
        let list = 'badge font-normal';

        if(this.isSoft()) {
            list += ' badge-soft';
        }

        if(this.isOutlined()) {
            list += ' badge-outline';
        }

        switch(this.size()) {
            case 'medium':
                list += ' badge-md';
                break;
            case 'small':
                list += ' badge-sm';
                break;
            case 'extra-small':
                list += ' badge-xs';
                break;
            default:
                list += ' badge-xs';
                break;
        }

        switch(this.type()) {
            case 'primary':
                list += ' badge-primary';
                break;
            case 'secondary':
                list += ' badge-secondary';
                break;
            case 'accent':
                list += ' badge-accent';
                break;
            case 'info':
                list += ' badge-info';
                break;
            case 'success':
                list += ' badge-success';
                break;
            case 'warning':
                list += ' badge-warning';
                break;
            case 'error':
                list += ' badge-error text-base-100';
                break;
            default:
                list += ' badge-primary';
                break;
        }

        return list;
    });
}
