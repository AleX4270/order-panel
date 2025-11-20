import { Component, computed, effect, input, InputSignal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TileType } from '../../enums/enums';

@Component({
    selector: 'app-tile',
    imports: [CommonModule],
    template: `
        <span
            [ngClass]="tileClassList()"
        >
            <ng-content/>
        </span>
    `,
    styles: [`
        .tile-label {
            font-weight: var(--font-weight-medium);
        }
    `]
})
export class TileComponent {
    public type: InputSignal<any> = input<any>(TileType.primary);
    protected tileClassList = computed(() => {
        let list = 'badge tile-label';

        switch(this.type()) {
            case TileType.primary:
                list += ' text-bg-primary';
                break;
            case TileType.secondary:
                list += ' text-bg-secondary';
                break;
            case TileType.success:
                list += ' text-bg-success';
                break;
            case TileType.danger:
                list += ' text-bg-danger';
                break;
            case TileType.warning:
                list += ' text-bg-warning';
                break;
            case TileType.info:
                list += ' text-bg-info';
                break;
            case TileType.light:
                list += ' text-bg-light';
                break;
            case TileType.dark:
                list += ' text-bg-dark';
                break;
        }

        return list;
    });
}
