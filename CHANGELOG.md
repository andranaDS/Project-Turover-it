# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - 2022-11-29
## Added
- FolderType : rename enum value from "yesterday-cart" to "yesterday_cart"

## [Unreleased] - 2022-11-28
## Added
- Put a JobPosting in favorites `PATCH /job_postings/{id}/favorite`
- List the Recruiter's favorite JobPostings `GET /job_postings/favorites`

## [Unreleased] - 2022-11-24
## Added
- Add filter `?q=` to query on `title` and `reference` of JobPostings like `GET /recruiters/me/job_postings?q=query`

## Edited
- Update `JobPosting::status` values to `draft, published, private, inactive`

## [Unreleased] - 2022-11-23
## Added
- Get Recruiter's folders list `GET /folders`

## [Unreleased] - 2022-11-21
## Added
- Add a trace on a JobPosting for a Recruiter `POST /job_postings/{id}/trace`

## [Unreleased] - 2022-11-18
## Added
- Property `viewsCount` on `GET /companies/mine/job_postings` & `GET /recruiters/me/job_postings`

## [Unreleased] - 2022-11-16
## Edited
- Property `companyBusinessActivity` changed to `businessActivity` of JobPostingSearchRecruiterAlert, JobPostingSearchRecruiterLog and JobPostingSearchRecruiterFavorite.

### Removed
- Property `contracts` of JobPostingSearchRecruiterAlert, JobPostingSearchRecruiterLog and JobPostingSearchRecruiterFavorite.
- Property `minAnnualSalary` of JobPostingSearchRecruiterAlert, JobPostingSearchRecruiterLog and JobPostingSearchRecruiterFavorite.
- Property `maxAnnualSalary` of JobPostingSearchRecruiterAlert, JobPostingSearchRecruiterLog and JobPostingSearchRecruiterFavorite.

## [Unreleased] - 2022-11-15
### Added
- DELETE JobPostingSearchRecruiterFavorite `DELETE /job_posting_search_recruiter_favorites/{id}`
- GET JobPostingSearchRecruiterFavorite `GET /job_posting_search_recruiter_favorites/{id}`
- `GET /recruiters/me/notifications` is replaced by `GET /notifications`
- Get the unread notifications count `GET /notifications/unread/count`
- Mark all unread notifications as read `POST /notifications/read`

## [Unreleased] - 2022-11-14
### Added
- POST JobPostingSearchRecruiterFavorite `POST /job_posting_search_recruiter_favorites`

## [Unreleased] - 2022-11-10
### Added
- Post a Folder `POST /folders`
``` json
# input
{
  "name": "My folder name"
}

# output
{
  "@context": "/contexts/Folder",
  "@id": "/folders/1337",
  "@type": "Folder",
  "id": 1337,
  "name": "My folder name",
  "type": "personal',
  "usersCount": 0
}
```
- Get a Folder `GET /folders/{id}`
``` json
# output
{
  "@context": "/contexts/Folder",
  "@id": "/folders/4",
  "@type": "Folder",
  "id": 4,
  "name": null,
  "type": "favorites',
  "usersCount": 3
}
```
- Put a Folder `PUT /folders/{id}`
``` json
# input
{
  "name": "My folder name updated"
}

# output
{
  "@context": "/contexts/Folder",
  "@id": "/folders/1337",
  "@type": "Folder",
  "id": 1337,
  "name": "My folder name updated",
  "type": "personal',
  "usersCount": 0
}
```
- Delete a Folder `DELETE /folders/{id}`

- List of the JobPostingSearchRecruiterLog of a Recruiter `GET /recruiters/me/job_posting_search_logs`
- Get a JobPostingSearchRecruiterLog `GET /job_posting_search_recruiter_logs/{id}`
- Listener to create a Log on `GET /job_postings`

## [Unreleased] - 2022-11-04
### Added
- List of the JobPostings of a public Company `GET /companies/{slug}/job_postings`
- List of the Notifications of the logged Recruiter `GET /recruiters/me/notifications`

  Infinite scroll `GET /recruiters/me/notifications?id[lt]={last_id}`

  Filter by event  `GET /recruiters/me/notifications?event=application_new` ; Possible values ATM **application_new**, **application_abandoned**, **job_posting_draft_expiring_soon**, **subscription_ending_soon**

  Filter by events  `GET /recruiters/me/notifications?event[]=application_new&event[]=application_abandoned` 🤗 or `GET /recruiters/me/notifications?event=application_new,application_abandoned` 🤮

- Get a Notification  `GET /notifications/{id}`

## [Unreleased] - 2022-11-02
### Added
- Delete JobPostingSearchRecruiterAlert `DELETE /job_posting_search_recruiter_alerts/1`
- Add `title` to JobPostingSearchRecruiterAlert

## [Unreleased] - 2022-10-31
### Added
- List of the JobPostings assigned to the logged Recruiter `GET /recruiters/me/job_postings` 
- List of the JobPostings of the logged Recruiter's Company `GET /companies/mine/job_postings`
- Create and update JobPostingSearchRecruiterAlert  `POST /job_posting_search_recruiter_alerts` `PUT /job_posting_search_recruiter_alerts`

### Removed
- Property `shortDescription` of a JobPosting.
