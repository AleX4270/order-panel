import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrderMapComponent } from './order-map.component';

describe('OrderMapComponent', () => {
  let component: OrderMapComponent;
  let fixture: ComponentFixture<OrderMapComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OrderMapComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrderMapComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
