// 페이지
import HomeComponent from '../pages/HomeComponent.vue';
import ActivityComponent from "../pages/ActivityComponent.vue";
import ProfileComponent from "../pages/ProfileComponent.vue";
import SearchComponent from "../pages/SearchComponent.vue";
import BookcaseComponent from "../pages/BookcaseComponent.vue";
import BookcaseCreateComponent from "../pages/BookcaseCreateComponent.vue";
import BookcaseEditComponent from "../pages/BookcaseEditComponent.vue";
import BookcaseFormComponent from "../pages/BookcaseFormComponent.vue";

// 컴포넌트
import PostFormComponent from "./PostFormComponent.vue";
import FeedComponent from "./FeedComponent.vue";
import ModalComponent from "./ModalComponent.vue";
import CommentComponent from "./CommentComponent.vue";
import CommentForm from "./CommentForm.vue";
import CommentListComponent from "./CommentListComponent.vue";
import AvatarComponent from "./AvatarComponent.vue";
import FollowerComponent from "./FollowerComponent.vue";
import swiperComponent from "./SwiperComponent.vue";
import HeaderComponent from "./headerComponent.vue";

//버튼
import LikeButton from "./buttons/LikeButton.vue";
import CommentButton from "./buttons/CommentButton.vue";
import ShareButton from "./buttons/ShareButton.vue";
import followToggleButton from "./buttons/FollowToggleButton.vue";
import dropdownComponent from "./DropdownComponent.vue";
import UserActionButton from "./buttons/UserActionButton.vue";
import HistoryBackButton from "./buttons/HistoryBackButton.vue";


//모달
import CommentModalComponent from "./CommentModalComponent.vue";
import ProfileModalComponent from "./ProfileModalComponent.vue";


export default {
    // 페이지
    "home-component": HomeComponent,
    "activity-component": ActivityComponent,
    "profile-component": ProfileComponent,
    "search-component": SearchComponent,
    "bookcase-component": BookcaseComponent,
    "bookcase-create-component": BookcaseCreateComponent,
    "bookcase-edit-component": BookcaseEditComponent,
    "bookcase-form-component": BookcaseFormComponent,

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
    "swiper-component": swiperComponent,
    "header-component": HeaderComponent,
    //버튼
    "like-button": LikeButton,
    "comment-button": CommentButton,
    "share-button": ShareButton,
    "follow-toggle-button": followToggleButton,
    "dropdown-component": dropdownComponent,
    "user-action-button": UserActionButton,
    "history-back-button": HistoryBackButton,
    // 모달
    "comment-modal-component": CommentModalComponent,
    "profile-modal-component": ProfileModalComponent,
}
