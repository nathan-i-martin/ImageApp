# Welcome to Image-App
This is the most bestest image app ever!

# Things to try:
## Create a new user
1. Click on `Register` or navigate to `register.php`
2. Enter a username and password! (See what happens when the username is already used! Try "_nathan_"!) (See what happens when the passwords don't match!)
3. When you create the new user, do you get logged in automatically?
4. This project doesn't have a page for searching and finding users. To follow a user, use this MySQL command:
```sql
SET @yourUserID := 1; -- Change to match your user's ID
SET @theirUserID := 1; -- Change to match the user you want to follow!
INSERT INTO usersFollowing (rootUser_fk, followedUser_fk)
VALUES (@yourUserID,@userId);
```
5. Now go to your feed and refresh! The user you followed should have their posts show up now! You can always unfollow a user from your feed!
6. Navigate to someone's profile! Check out their posts, you can also follow/unfollow them!

## Create a post
1. From your feed, click on the `+` icon!
2. Upload an image!
3. Enter some details about your post!
4. After posting, you can check out your post by going to your profile (click your icon in the navbar)

## Edit your profile
1. Check out your profile (click your icon in the navbar)
2. Click "Edit Profile"
3. Try editing things! Upload a new profile photo, change the username/description. Try changing your password!

# Don't be shy
Feel free to mess around! You can always reset all the data using the `finalProject.sql` startup script!
Try doing some stuff on one user account and then log into another user account and see what's changed!

# Login info
All user accounts that load from the startup script load with the password of `123`. The initial user accounts that load are:

```yml
Username: password

_nathan_: 123
  john10: 123
 cole-32: 123
```