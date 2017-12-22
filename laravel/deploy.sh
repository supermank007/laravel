#
#
# Patient Forms Sever Deploy Script
#
#

COLOR='\033[1;34m'
NC='\033[0m' # No Color

echo_color () {
    echo -e "${COLOR}$1${NC}"
}

echo
echo_color "Updating svn..."
echo
cd /home/patientforms/public_html/dev.patientforms.net/laravel/svn
svn update
echo
echo_color "Updated svn."
echo '--------------------'

echo_color "Copying repo to webroot..."
echo
cd ../
cp svn/* site/patientforms -r
echo
echo_color "Copied repo to webroot."
echo '--------------------'

cd site/patientforms

echo_color "Running migrations..."
echo
php artisan migrate
echo
echo_color "Ran migrations."
echo '--------------------'

function db_seed
{
    echo_color "Seeding database..."
    echo
    php artisan db:seed
    echo
    echo_color "Seeded database."
    echo '--------------------'
}

function migrate_refresh
{
    echo_color "Refreshing migrations..."
    echo
    php artisan migrate:refresh
    echo
    echo_color "Refreshed migrations."
    echo '--------------------'
}

function composer_update
{
    echo_color "Running composer update..."
    echo
    php composer.phar update
    echo
    echo_color "Ran composer update."
    echo '--------------------'
}

    

while [ "$1" != "" ]; do
    case $1 in
        --seed )       db_seed
                       ;;
        --refresh )    migrate_refresh
                       ;;
        --composer )   composer_update
                       ;;
    esac
    shift
done
