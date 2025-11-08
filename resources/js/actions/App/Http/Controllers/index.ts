import Settings from './Settings'
import TokenController from './TokenController'

const Controllers = {
    Settings: Object.assign(Settings, Settings),
    TokenController: Object.assign(TokenController, TokenController),
}

export default Controllers