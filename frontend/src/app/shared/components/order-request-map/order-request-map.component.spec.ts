import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrderRequestMapComponent } from './order-request-map.component';

describe('OrderRequestMapComponent', () => {
  let component: OrderRequestMapComponent;
  let fixture: ComponentFixture<OrderRequestMapComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OrderRequestMapComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrderRequestMapComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
