import * as aws from '@pulumi/aws';

const bucket = new aws.s3.Bucket(process.env.BUCKET_ID);

new aws.s3.BucketPublicAccessBlock(
  'public-access-block',
  {
    bucket: bucket.id,
    blockPublicAcls: false,
  },
  { dependsOn: bucket },
);

export const bucketName = bucket.id;

new aws.s3.BucketPolicy('my-bucket-policy', {
  bucket: bucket.bucket,
  policy: bucket.bucket.apply((bucketName: string) =>
    JSON.stringify({
      Version: '2012-10-17',
      Statement: [
        {
          Effect: 'Allow',
          Principal: '*',
          Action: ['s3:GetObject'],
          Resource: [`arn:aws:s3:::${bucketName}/*`],
        },
      ],
    }),
  ),
});
