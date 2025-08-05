import { Injectable } from '@angular/core';
import { State, Selector, Action, StateContext } from '@ngxs/store';
import { initialUserState, UserStateModel } from './user.state.model';
import { LoginUser, LogoutUser, SetUserLanguage } from './user.actions';
import { LanguageType } from '../../enums/enums';
import { User } from '../../types/user.types';

@State<UserStateModel>({
    name: 'user',
    defaults: {
        ...initialUserState
    }
})
@Injectable()
export class UserState {
    @Action(LoginUser)
    public loginUser(ctx: StateContext<UserStateModel>, payload: LoginUser) {
        ctx.patchState({
            ...payload.data
        });
    }

    @Action(LogoutUser)
    public logoutUser(ctx: StateContext<UserStateModel>) {
        const currentLanguage = ctx.getState().language;
        let defaultValues = initialUserState;

        if(currentLanguage) {
            defaultValues.language = currentLanguage;
        }

        ctx.patchState({
            ...defaultValues
        });
    }

    @Action(SetUserLanguage)
    public setUserLanguage(ctx: StateContext<UserStateModel>, payload: SetUserLanguage) {
        ctx.patchState({
            ...ctx.getState(),
            language: payload.languageSymbol
        });
    }

    @Selector([UserState])
    static isAuthenticated(state: UserStateModel): boolean {
        return state.isAuthenticated;
    }

    @Selector([UserState])
    static userLanguage(state: UserStateModel): LanguageType | undefined {
        return state.language;
    }

    @Selector([UserState])
    static userData(state: UserStateModel): User {
        return state;
    }
}
