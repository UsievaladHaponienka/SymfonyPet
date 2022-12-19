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

`Group Invite` are like reversed `Group Request` - `Invite` is created by Group admin at `Manage Group`->`Send Invites` 
page (And can be canceled by admin at `Manage Group`->`Invited Users` page) and need to be accepted (and also can be
declined) by invited User at `My Groups`->`Group Invites` page. The main difference between `Invite` and `Group Request`
is that `Invite` can be created for both Group types - public AND private, while `Group Request` can be created (and 
makes sense) for `private` Groups only.

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
be added to this album (though existing `Photos` can be deleted). These albums are used to store Post's photos (see below).
such albums have `type` = `user_posts` (if `Album` belongs to `Profile`) or `group_posts` (if `Album` belongs to
`Group`). 

Default album is created right after successful `Profile`/`Group` creation. Both `Profile` and `Group` entities have 
method `getDefaultAlbum` (see `\App\Entity\Traits\HasDefaultAlbum`).

Note that deleting `Photo` from default `Album` won't delete corresponding `Post`, but deleting `Post` will delete
`Photo`.


#### Custom albums

`Profile` and `Group` can have custom albums (with `type` = `user_custom` and `group_custom`). This albums can be
deleted, edited, filled with `Photos` by `Profile` owner or `Group` admin respectively.

Note that deleting `Album` will automatically delete all corresponding `Photos`.


## Navigation

Navigation tab is displayed at the left part of each page (except for login and registration pages). It contains the
following links:

- My Profile
- My Feed
- My Friends
- My Albums
- My Groups
- Edit profile
