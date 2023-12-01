#!/bin/bash

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer is not installed. Please install Composer before running this script."
    exit 1
fi

# Install the CLI tool globally
#composer global require compo/cyrex

# Add Composer's global bin directory to the user's PATH
if ! grep -q ".composer/vendor/bin" "$HOME/.bashrc"; then
    echo 'export PATH=$PATH:$HOME/.composer/vendor/bin' >> "$HOME/.bashrc"
    source "$HOME/.bashrc"
    echo "Updated your .bashrc file to include Composer's global bin directory in your PATH."
fi

echo "Installation complete. You can now use your CLI tool by running 'tool-name'."

