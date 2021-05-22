import Home from './components/views/Home';
import About from './components/views/About';
import Calendar from './components/views/Calendar';
import Profile from './components/views/Profile';
import Settings from './components/views/Settings'
import Event from './components/views/events/Event'
import CreateEvent from './components/views/events/CreateEvent';
import EditEvent from './components/views/events/EditEvent';
import NotFound from './components/views/NotFound';

export default {
    mode: 'history',
    linkActiveClass: '',

    routes: [
        {
            path: '*',
            component: NotFound
        },
        {
            name: 'home',
            path: '/',
            component: Home
        },
        {
            path: '/about',
            component: About
        },
        {
            path: '/calendar',
            component: Calendar
        },
        {
            path: '/profile',
            component: Profile
        },
        {
            path: '/settings',
            component: Settings
        },
        {
            name: 'create',
            path: '/create',
            component: CreateEvent
        }, 
        {
            name: 'edit',
            path: '/edit/:id',
            component: EditEvent
        },
        {
            name: 'event',
            path: '/event/:id',
            component: Event
        }

    ]
}