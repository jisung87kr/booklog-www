// 페이지
import HomeComponent from '../pages/HomeComponent.vue';
import ActivityComponent from "../pages/ActivityComponent.vue";
import ProfileComponent from "../pages/ProfileComponent.vue";
import SearchComponent from "../pages/SearchComponent.vue";

// 컴포넌트
import PostFormComponent from "./PostFormComponent.vue";
import FeedComponent from "./FeedComponent.vue";
import ModalComponent from "./ModalComponent.vue";
import CommentComponent from "./CommentComponent.vue";
import CommentForm from "./CommentForm.vue";
import CommentListComponent from "./CommentListComponent.vue";
import AvatarComponent from "./AvatarComponent.vue";
import FollowerComponent from "./FollowerComponent.vue";

//버튼
import LikeButton from "./buttons/LikeButton.vue";
import ShareButton from "./buttons/ShareButton.vue";
import followToggleButton from "./buttons/FollowToggleButton.vue";
import dropdownComponent from "./DropdownComponent.vue";
import UserActionButton from "./buttons/UserActionButton.vue";


export default {
    // 페이지
    "home-component": HomeComponent,
    "activity-component": ActivityComponent,
    "profile-component": ProfileComponent,
    "search-component": SearchComponent,
    // 컴포넌트
    "post-form-component": PostFormComponent,
    "feed-component": FeedComponent,
    "modal-component": ModalComponent,
    "comment-component": CommentComponent,
    "comment-form": CommentForm,
    "comment-list": CommentListComponent,
    'modal-Component': ModalComponent,
    "avatar-component": AvatarComponent,
    "follower-component": FollowerComponent,
    //버튼
    "like-button": LikeButton,
    "share-button": ShareButton,
    "follow-toggle-button": followToggleButton,
    "dropdown-component": dropdownComponent,
    "user-action-button": UserActionButton,
}
