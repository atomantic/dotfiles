#!/bin/bash

# Define the directory to search and the source of the post-commit hook
SEARCH_DIR="$(pwd)"                                        # Directory containing the Git repositories to update
POST_COMMIT_SOURCE="$HOME/.git_template/hooks/post-commit" # The path to the global post-commit hook

# Ensure the post-commit source exists
if [ ! -f "$POST_COMMIT_SOURCE" ]; then
  echo "Error: post-commit hook source not found at $POST_COMMIT_SOURCE"
  exit 1
fi

# Update git config to use the template directory
git config --global init.templateDir "$HOME/.git_template"

# Read the content of the source post-commit hook
post_commit_content=$(cat "$POST_COMMIT_SOURCE")

# Find all Git repositories in the given directory
find "$SEARCH_DIR" -type d -name ".git" | while read -r git_dir; do
  # Determine the repository root and the hooks directory
  repo_root=$(dirname "$git_dir")
  hooks_dir="$git_dir/hooks"
  husky_dir="$repo_root/.husky"

  if [ -d "$husky_dir" ]; then
    # If .husky directory exists, use the .husky/post-commit hook
    post_commit_hook="$husky_dir/post-commit"

    if [ -e "$post_commit_hook" ]; then
      # If .husky post-commit already exists, check if it contains the source content
      if ! grep -Fq "$post_commit_content" "$post_commit_hook"; then
        # Append the source content if it does not exist already
        echo -e "\n$post_commit_content" >>"$post_commit_hook"
        echo "Appended post-commit hook content for repository with Husky at $repo_root"
      else
        echo "Husky post-commit hook already contains the required content for repository at $repo_root"
      fi
    else
      # If .husky post-commit does not exist, create it with the source content
      echo -e "#!/bin/sh\n" >"$post_commit_hook"
      echo -e "$post_commit_content" >>"$post_commit_hook"
      chmod +x "$post_commit_hook"
      echo "Created new Husky post-commit hook for repository at $repo_root"
    fi
  else
    # If no .husky directory, use the .git/hooks/post-commit hook
    post_commit_hook="$hooks_dir/post-commit"

    if [ -e "$post_commit_hook" ]; then
      # If post-commit already exists, check if it contains the source content
      if ! grep -Fq "$post_commit_content" "$post_commit_hook"; then
        # Append the source content if it does not exist already
        echo -e "\n$post_commit_content" >>"$post_commit_hook"
        echo "Appended post-commit hook content for repository at $repo_root"
      else
        echo "Post-commit hook already contains the required content for repository at $repo_root"
      fi
    else
      # If post-commit does not exist, create it with the source content
      cp "$POST_COMMIT_SOURCE" "$post_commit_hook"
      chmod +x "$post_commit_hook"
      echo "Created new post-commit hook for repository at $repo_root"
    fi
  fi
done
