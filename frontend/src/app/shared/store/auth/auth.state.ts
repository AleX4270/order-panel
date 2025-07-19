import { Injectable } from '@angular/core';
import { State, Selector, Action, StateContext } from '@ngxs/store';
import { AuthStateModel } from './auth.state.model';
import { LoginUser, LogoutUser } from './auth.actions';

@State<AuthStateModel>({
    name: 'auth',
    defaults: {
        user: null,
        isAuthenticated: false,
    }
})
@Injectable()
export class AuthState {
    @Action(LoginUser)
    public loginUser(ctx: StateContext<AuthStateModel>, action: LoginUser) {
        ctx.patchState({
            user: action.user,
            isAuthenticated: true
        });
    }

    @Action(LogoutUser)
    public logoutUser(ctx: StateContext<AuthStateModel>) {
        ctx.setState({
            user: null,
            isAuthenticated: false
        });
    }

    @Selector([AuthState])
    static isAuthenticated(state: AuthStateModel): boolean {
        return state.isAuthenticated;
    }
}
