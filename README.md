# This is demo social network created using Symfony Framework

## Important

I'm a PHP backend developer who is studying Symfony right now. All frontend (both CSS using Tailwind and JS) is probably
a set of stupid workarounds and quality of frontend code is very low in general. The goal of this project was to study
backend component of Symfony and create corresponding *backend* demo.

## Authentication

By default, website is not available for unauthenticated user. If unauthenticated user tries to access some page, he/she
will be redirected to login page. `\App\EventListener\LoginRedirectListener` is responsible for that behaviour.

## Used entities

### Profile

`Profile` is an entity which represents user of social network. It is separated from `User` entity as `User` is mainly
used for authentication, while `Profile` is used for all other website relations and interactions. `Profile` is created
automatically after user's successful registration. Each `User` has only one `Profile`, each `Profile` belongs to only
one `User`.

Profile entity has:

- Profile username. Not required, but recommended to have not empty.
- Profile description. Not required.
- Profile image. Not required.

All this properties can be edited by Profile owner at `Edit Profile` page.

### Friendship and Friendship request

Users can become `friends` with each other. Standard friendship creation flow is the following:

1. User 1 visits User 2 profile page and clicks `Add to friends` button. This action creates entity called `Friendship
   Request`. Friendship request contains only two fields:
    - `requester_id`, id of `Profile` *which created* the request.
    - `requestee_id`, id of `Profile` *to which* the request was created.

   User 1 also can click `Cancel request` button after request was created. This will delete corresponding `Friendship
   Request` entity.

2. User 2 visits `My friends` -> `Incoming Requests` page and clicks `Accept request` button. After that `Friendship
   Request` will be deleted and `Friendship` entity will be created instead. Note that each friendship is represented
   in database with two lines in `friendship` table, not just one. Line one "says" that "User 1 is friend of User 2",
   line
   two "says" that "User 2 is friend of User 1". This is kind of data duplication, but it makes easier to get list of
   User's friends where you need it.

   User 2 can also click `Decline request` button. In this case `Friendship Request` will be deleted, `Friendship`
   entity won't be created.

Friendship also can be deleted by any of friends (this action is called "Remove from friends").

### Group, Group Request and Group Invite

Each user can create multiple groups. Groups allow users with common interests to join together.

`Group` entity has:

- Title.
- Description.
- Group image.
- Type.
- Admin id.

Admin id is link to admin Profile. Right now it's OneToMany relation, Group can have ONLY one admin - Profile who
created the group.

Group type can be `public` or `private`.

#### Public group

Public group page and related pages (see below) can be viewed by anyone.
User can join group by simply clicking `Join` button at group page.

#### Private group

Private group page and related pages (see below) can be viewed only by group members.
Private group join works similar to friendship creation, but `Group Request` entity is created instead of `Friendship
Request`. `Group Request` contains link to `Profile` who made request and `Group` which was requested.

1. User 1 click's `Join group` button.
2. User 2, admin of corresponding group, visits `Manage Group`->`Group Requests` page and accepts join request.

Similar to friendship, `Group request` can be canceled by `Profile` who made request and also can be declined
by `Group` admin.

`Group Invite` is like reversed `Group Request` - `Invite` is created by Group admin at `Manage Group`->`Send Invites`
page (And can be canceled by admin at `Manage Group`->`Invited Users` page) and need to be accepted (and also can be
declined) by invited User at `My Groups`->`Group Invites` page. The main difference between `Invite` and `Group Request`
is that `Invite` can be created for both Group types - public AND private, while `Group Request` can be created (and
makes sense) for `private` Groups only.

`Group` membership is stored in `group_profile` table.

### Album

`Album` is an entity which is used to organize `Photo's` (see below). Album has the following fields:

- Title
- Description
- Type
- Profile id, if album belongs to profile
- Related group id, if album belongs to group

Though there is only one class which represents Album, multiple album types are used.

#### Default album

Each `Profile` and each `Group` has default `Album`. This album can not be edited, deleted and also new `Photos` can not
be added to this album manually (though existing `Photos` can be deleted). These albums are used to store Post's
photos (see below).
Such albums have `type` = `user_posts` (if `Album` belongs to `Profile`) or `group_posts` (if `Album` belongs to
`Group`).

Default album is created right after successful `Profile`/`Group` creation. Both `Profile` and `Group` entities have
method `getDefaultAlbum` (see `\App\Entity\Traits\HasDefaultAlbum`).

Note that deleting `Photo` from default `Album` won't delete corresponding `Post`, but deleting `Post` will delete
`Photo`.

#### Custom albums

`Profile` and `Group` can have custom albums (with `type` = `user_custom` and `group_custom`). This albums can be
deleted, edited, filled with `Photos` by `Profile` owner or `Group` admin respectively.

Note that deleting `Album` will automatically delete all corresponding `Photos`.

### Post

`Post` is one of the main entities at website and main way for Users to create content.

Each `Post` MUST have either image or content and can have both.

Though there is only one class to represent `Post`, there are two different types of `Posts` - `profile` and `group`.

#### Profile post

Profile `Post` is created by Profile using post form at Profile page. It has `Profile` info (Profile image and
username) and `profile_id` in `post` table. This post can be deleted by related `Profile` owner.

#### Group post

Group `Post` is created by group admin using post form at `Group` page. It has `Group` info (Group image and title) and
`related_group_id` in `post` table. This post can be deleted by Group admin.

Even if `Post` has image, it doesn't contain image URL. Instead, image is stored as `Photo` entity which relates
to `Post`. `Post` images can be viewed (and even deleted) in default album (see `Album`).

### Discussion

`Discussion` is a Group-related entity which allows group members to discuss different topics in Group. `Discussion` has
title and description. `Discussions` can be created/deleted only by Group admin. Each discussion can have `Comments` (
see `Comment`) left by other users.

`Discussion` view and commenting rules depend on related `Group` type and use similar logic:

- `Discussion` from public `Group` can be viewed and commented by anyone.
- `Discussion` from private `Group` can be viewed and commented only by `Group` members.

### Photo

`Photo` represents image, which is not used as `Profile` or `Group` image. Each `Photo` belongs to `Album`. `Photos` are
either added to `Album` (but only if album has `group_custom` or `user_custom` type, see `Album`) by Profile
owner/Group admin or added automatically to default profile/group album when new `Post` is created. If `Photo` has
related `Post`, Post `content` also stored as Photo `description`.

All Photos images are automatically resized to 1200x1200 resolution which makes non-square images look wierd, but this
is Symfony Demo, not Image Resizing one :)

Each `Photo` entity has only one image.
Each `Photo` entity is related to only one `Album`.
Each `Post` can have only one `Photo`.

### Comment

`Comment` is an entity which allows users to react to `Posts` and participate in `Discussions`. So there are two
different types of `Comments` - Post comments and discussion comments. Each comment belongs to Profile, it is not
possible to create comment from Group.

`Comment` can be deleted either by Comment author, or by Post Profile owner, or by Post Group admin (see `Summarized
entity interaction rules`).

### Like

`Like` is and entity which allows users to react to `Posts` or `Comments` in most simple way. So, there are two types of
likes - Post likes and Comment likes.

Each `Post`/`Comment` has amount of likes displayed below its content. Pressing button "Like" will create `Like` entity
related to Profile and `Post`/`Comment`. Pressing this button again will remove corresponding `Like` entity.

Tou always know whether you liked some `Post`/`Comment` or not: If entity wasn't already liked, button title is "Like"
and button is dark-blue. If entity was already liked, Button title is "Liked" and button color is light-blue.

### Profile Privacy Settings

`Profile Privacy Settings` is a service entity which is used to restrict access to certain parts of `Profile` page. It
has 4 fields and 3 possible options for each field.

Fields are:

- Who can view my friends
- Who can view my groups
- Who can view my albums
- WHo can view my posts

Options are:

- Only me
- Only my friends
- Anyone

Seems like fields and options speak for themselves. The only thing which is worth mentioning here that `Only my friends`
option actually means `Me + My Friends`. See `Summarized entity interaction rules` for more details.

### Entities relation visualization

Entities relations are visualized here: https://www.plectica.com/maps/6ZKXDDCD7.

## Summarized entity interaction rules

### Profile

- Profile is created automatically after User creation (i.e. after successful registration).
- Profile can be edited (both profile data and Profile Privacy Settings) by Profile owner.
- Profile can not be deleted.

### Friendship request

- Friendship request can be created by ANY Profile to ANY Profile (Of course, if this two profiles are not friends yet
  and request wasn't already created).
- Friendship request can be deleted either by request sender (requester) which means "Cancel Request" action or by
  request receiver (requestee) which corresponds to "Decline Request" action. Friendship is also automatically deleted
  when `Friendship` is created.

### Friendship

- Friendship is created when Friendship Request requestee accepts request.
- Friendship can be deleted by any of friends ("Remove from friends" action).

### Group

- Group can be created by any user. User's Profile becomes Group admin.
- Group can have only one admin.
- Group can be edited only by admin.
- Group can be deleted only by admin.

### Group Request

- Group request can be created for private Group.
- Group request can be created by Profile only if:
    - Profile isn't already a member of the Group.
    - Same Request (from the same Profile to the same Group) wasn't already created.
    - Similar Invite (form the same Group to the same Profile) wasn't already created.
- Group request can be deleted either by requester (Profile owner), which corresponds to "Cancel request" action, or by
  requested group admin, which corresponds to "Decline request" action. Group request is automatically deleted when
  accepted by Group Admin as group membership relation is crated instead.
- Group requests are deleted automatically when `Group` is deleted.

### Group Invite

- Group invite can be created both for public and private Groups.
- Group invite can be created by Group admin if:
    - Invite receiver (Profile) isn't already a member of the Group.
    - Same Invite (From the same Group to the same Group) wasn't already created.
    - Similar Request (from the same Profile to the same Group) wasn't already created.
- Group request can be deleted either by Group admin, which corresponds to "Cancel Invite" action, or by
  Invite recipient (Profile), which corresponds to "Decline invite" action. Group invite is automatically deleted when
  accepted by invited User as group membership relation is crated instead.
- Group invites are deleted automatically when `Group` is deleted.

### Group Membership

Group membership is not a separate entity, it's ManyToMany relation between Group and Profile.

Group membership is created when:

- User uses "Join" button (only fo `public` Groups).
- Group Request is accepted by Group admin (only for `private` Groups).
- Group Invite is accepted by invited Profile (for both `public` and `private` Groups).

Group membership can be deleted:

- By Profile, using "Leave group" button.
- By Group admin using `Manage Group`->`Group Members`->Remove from Group button.
- Group memberships are deleted automatically when `Group` is deleted.

### Album

- Default album:
    - Default Profile/Group Album is created automatically after Profile/Group is created. This album can not be edited
      or deleted, `Photos` can not be added to this album.
    - However, `Photos` from default Profile/Group Album can be deleted by Profile owner/Group admin respectively. This
      action doesn't lead to deletion of the corresponding `Post`.
    - `Photo` from default Album is automatically deleted when corresponding Post is deleted.
- Custom Album:
    - Custom Profile/Group Album can be created, edited and deleted by Profile owner/Group admin.
    - Profile owner/Group admin can add photos to Profile/Group custom album.
- Both types of albums:
    - Group albums list and Group albums Photos view rule are the same as Group view rules - either if `Group` is public
      or User is the member of the `Group`.
    - Profile albums list and Profile albums Photos view rules are determined by `Profile Privacy Settings`.

### Discussion

- Discussion can be created and deleted only by Group admin.
- Discussion view and commenting rules are the same as for `Group` - either `Group` is public or User is the member of
  the `Group`.

## Navigation

Navigation tab is displayed at the left part of each page (except for login and registration pages). It contains the
following links:

- My Profile
- My Feed
- My Friends
- My Albums
- My Groups
- Edit profile

https://www.plectica.com/maps/6ZKXDDCD7
