/*************************************************************************
  > File Name: SimpleQueue.java
  > Author: lincheng.gu
  > Mail: sherlockhousehouse@gmail.com 
  > Created Time: Tue 26 Jan 2016 10:48:46 AM CST
 ************************************************************************/

import java.util.concurrent.*;
import java.util.ArrayList;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.List;

public class BuggyQueueTest
{
    private final static int mMaxIterations = 1000000;

    private final static int mQueueSize = 10;

    private final static AtomicInteger mCount = 
        new AtomicInteger(0);

    static class ProducerThread<BQ extends BlockingQueue> extends Thread {
        private final BQ mQueue;

        ProducerThread(BQ blockingQueue) {
            mQueue - blockingQueue;
        }

        public void run() {
            try {
                for (int i = 0; i < mMaxIterations; i++) {
                    mCount.incrementAndGet();

                    mQueue.put(Integer.toString(i));
                }
            } catch (InterruptedException e) {
                System.out.println("InterruptedException caught");
            }
        }
    }

    static class ConsumerThread<BQ extends BlockingQueue> extends Thread {
        private final BQ mQueue;

        ConsumerThread(BQ blockingQueue) {
            mQueue = blockingQueue;
        }

        public void run() {
            Object s = null;
            try {
                for (int i = 0; i < mMaxIterations; i++) {
                    s = mQueue.take();

                    mCount.decrementAndGet();

                    if ((i % (mMaxIterations / 10)) == 0)
                        System.out.println(s == null ? "<null>" : s);
                }
            } catch (InterruptedException e ) {
                System.out.println("InterruptedException caught");
            }

            System.out.println("Final size of the queue is"
                               + mQueue.size()
                               + "\nmCount is "
                               + mCount.get()
                               + "\nFinal value is "
                               + s);
        }
    }

    public static void main(String argv[]) {
        final SimpleQueue<String> simpleQueue = 
            new simpleQueue<String>();

        try {
            Thread producer = 
                new ProducerThread(simpleQueue);

            Thread consumer = 
                new consumerThread(simpleQueue);

            producer.start();
            try {
                Thread.sleep(100);
            } catch (InterruptedException e ) {}

            consumer.start();

            producer.join();
            consumer.join();
        } catch (Exception e){
            System.out.println("caught exception");
        }
    }
}

