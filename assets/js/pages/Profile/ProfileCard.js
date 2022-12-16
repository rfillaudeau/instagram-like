import React, {useState} from "react"
import FollowButton from "../../components/FollowButton"
import abbreviateNumber from "../../utils/abreviateNumber"
import nl2br from "../../utils/nl2br"

function ProfileCard({user: propsUser}) {
    const [user, setUser] = useState(propsUser)

    function handleFollow(isFollowed) {
        setUser(prevUser => {
            let newUser = {
                ...prevUser,
                isFollowed: isFollowed
            }

            if (isFollowed) {
                newUser.followerCount++
            } else {
                newUser.followerCount--
            }

            return newUser
        })
    }

    return (
        <div className="card mb-2">
            <div className="card-body">
                <div className="d-flex">
                    <div className="me-3">
                        <img
                            src={user.avatarFilepath}
                            className="rounded img-fluid avatar-lg"
                            alt={`${user.username}'s avatar`} />
                    </div>

                    <div className="flex-fill">
                        <div className="d-flex mb-3">
                            <div className="flex-fill">
                                <span className="fs-2 fw-semibold">{user.username}</span>
                            </div>
                            <div className="align-self-center">
                                <FollowButton
                                    user={user}
                                    onFollow={() => handleFollow(true)}
                                    onUnfollow={() => handleFollow(false)}
                                />
                            </div>
                        </div>

                        <div className="row mb-3">
                            <div className="col">
                                <b>{abbreviateNumber(user.postCount)}</b> post{user.postCount > 1 ? "s" : ""}
                            </div>
                            <div className="col text-center">
                                <b>{abbreviateNumber(user.followerCount)}</b> follower{user.followerCount > 1 ? "s" : ""}
                            </div>
                            <div className="col text-end">
                                <b>{abbreviateNumber(user.followingCount)}</b> following
                            </div>
                        </div>

                        <p className="mb-0">{nl2br(user.bio)}</p>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default ProfileCard
